/**
*	
*	CCombo v1.0
*	
*	Copyright (c) 2010-2011, Chris Tsamsakizoglou
*	
*	Dual licensed under the MIT and GPL licenses.
*	http://www.opensource.org/licenses/mit-license.php
*	http://www.gnu.org/licenses/gpl.html
*	
*	
*	Creation date:	25/08/2010
*	Release date:	08/01/2011
*/

var Combos={
	combos:new Object(),
	get_combo:function(id){
		return this.combos[id];
	},
	add_combo:function(combo){
		this.combos[combo.id]=combo;
	},
	remove_combo:function(combo){
		delete this.combos[combo.id]
	},
	getAutoId:function(){
		return "combo_"+new Date().getTime();
	}
};

(function($) {
	var KEY_CODES = {
			UP: 38,
			DOWN: 40,
			LEFT: 37,
			RIGHT: 39,
			TAB: 9,
			ENTER: 13,
			ESC: 27,
			PAGE_UP: 33,
			PAGE_DOWN: 34
		};
	var CHAR_KEY_CODES=[8,45,46,48,49,50,51,52,53,54,55,56,57,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,96,97,98,99,100,101,102,103,104,105,106,107,109,110,111,186,187,188,189,190,191,192,219,220,221,222];
	var TYPE={
			OPEN_LIST:0,
			OPEN_TREE:1,
			SCROLL:2,
			OPEN_SEARCH:3
	}
	var ICON_SIZE=11;
	var themes_folder="themes/";

	$.fn.combo = function(options) {
		var length=this.length;
		if(length>0) {
			var curCombos=[];
			for(var i=0; i<length; i++){
				var id=options && options.id?options.id:this[i].id;
				if(!id){
					id=Combos.getAutoId();
				}
				if(!Combos.get_combo(id)){
					var comb = new Combo(id, this[i], options);
					Combos.add_combo(comb);
					curCombos[i]=comb;
					comb.init();
				}else{
					if (window.console){
						try{
							console.log("Combo object with id '"+id+"' already exists in the stack");
						}catch(err){}
					}
				}
			}
			return curCombos;
		}
	};
	
	
	function Combo(aid, obj, options) {
		this.defaults = {
			max_height: 10, //list height
			page_step: 10,		//step when press page up/down
			select_categories:true, //define whether the parent of a category can be selected
			icons:{collpase:"CollapseTree.gif", expand:"ExpandTree.gif"},
			skin:'default',	//combo look and feel
			//basepath:'/',	//basepath for themes and icons
			auto_close:true,	//if list closes automatically
			editable:false,	//editable input?
			width:150	//width of input
		};
		this.opts = $.extend({},this.defaults,options);	
		this.id=aid;	//component id
		var el;
		var input;	//the input
		var arrow;	//combo arrow
		var wrapper; //wrapper for the list elements
		var list;		//the list of elements
		var data=[];	//holds all elements
		var tmpData=[];	//holds matched elements
		var index=0;	//pointer n the data array. shows where we are now
		var ref=this;	//reference to this for functions
		var opened=false;	//whether the list is opened or not
		var item_height=19;	//height of li in px. For clever scroll
		var basepath='';
		
		this.init=function() {
			if(!window.basepath){
				window.basepath=getPath();
			}
			basepath=window.basepath;
			this.opts.icons.collpase=basepath+themes_folder+this.opts.skin+"/"+this.opts.icons.collpase;
			this.opts.icons.expand=basepath+themes_folder+this.opts.skin+"/"+this.opts.icons.expand;
			var html="<table cellspacing='0' cellpadding='0'><tbody><tr><td class='combo-left' style='background-image:url(\""+basepath+themes_folder+this.opts.skin+"/combo_left.png\");'></td><td class='combo-center' style='background-image:url(\""+basepath+themes_folder+this.opts.skin+"/combo_center.png\");'>"+
						(this.opts.editable?"<input type='text' class='combo-inp' style='width:"+this.opts.width+"px;'/>":"<span class='combo-inp' style='display:table-cell;vertical-align:middle;width:"+this.opts.width+"px;'></span>")+
						"</td><td class='combo-arrow' style='background-image:url(\""+basepath+themes_folder+this.opts.skin+"/combo_arrowUp.png\");'></td></tr></tbody></table>";
			html=jQuery("<span class='combo'></span>").append(html);
			wrapper=jQuery("<div class='combo-wrapper'></div>");
			html.append(wrapper);
			arrow=html.find('.combo-arrow');
			input=html.find('.combo-inp');
			el=jQuery(obj);
			if(obj.tagName=="SELECT" || obj.tagName=="INPUT"){
				el.css('display', 'none');
				el.after(html);
			}else{
				el.html(html);
			}
			this.addData();
			this.opts.width-=21;
			this.opts.max_height=item_height*this.opts.max_height;
			arrow.click(function(){
				if(!list){
					ref.open(false);
				}else{
					ref.close(true);
				}
			}).mouseover(function(){
				if(!list){
					jQuery(this).css('background-image', 'url('+basepath+themes_folder+ref.opts.skin+'/combo_arrowDown.png)');
				}
			}).mouseout(function(){
				if(!list){
					jQuery(this).css('background-image', 'url('+basepath+themes_folder+ref.opts.skin+'/combo_arrowUp.png)');
				}
			});
			if(this.opts.editable){
				input.bind('keydown', function(event){
						switch(event.keyCode){
							case KEY_CODES.ENTER:
									if(!opened){
										if(!list){
											ref.open(true);
										}else{
											ref.close(true);
										}
									}
								break;
							case KEY_CODES.DOWN:
								ref.open(true);
								break;
							default:
								if(jQuery.inArray( event.keyCode, CHAR_KEY_CODES )>=0){
									input.data('code', '');
								}
								break;
					}
					event.stopPropagation();
					event.preventDefault();
				}).bind('focus', function(){
					if(list && list.is(':visible')){
						ref.close(true);
					}
				});
			}else{
				input.bind('click', function(event){
					if(!list){
						ref.open(true);
					}else{
						ref.close(false);
					}
				});
			}
		}
		this.addData=function(){
			var sel;
			if(this.opts.selectId){
				sel=document.getElementById(this.opts.selectId);
				this.addSelect(sel);
			}else if(el.get(0).tagName=="SELECT"){
				sel=el.get(0);
				this.addSelect(sel);
			}else if(this.opts.url){
				var url=this.opts.url;
				var type=this.opts.dataType;
				if(type=="JSON"){
					jQuery.post(url, function(dat){
						ref.addJson(dat);
					}, "json");
				}else if(type=="XML"){
					jQuery.post(url,function(dat){
						ref.addXml(dat);
					}, "xml");
				}
			}
		}
		this.populateList=function(){
			list=jQuery('<div></div>').addClass('combo-list').css('display', 'none');
			var ul=jQuery("<ul></ul>");
			var length=0;
			if(tmpData.length==0){
				ref.populateLevel(-1, data, ul, TYPE.OPEN_LIST);
			}else{
				ref.populateLevel(-1, tmpData, ul, TYPE.OPEN_LIST);
			}
			list.append(ul);
			wrapper.html(list);
			list.bind('scroll', function(){
				var uel=list.find('ul');
				if ( Math.abs(uel.position().top) + list.height() + list.position().top >= uel.outerHeight() ) {
					ref.scrollItems(-1);
				}
			});
		}
		this.populateLevel=function(idex, dataTable, dest, type, until){
			var level;
			var lspace=ICON_SIZE;
			var length=dataTable.length;
			if(tmpData.length>0){
				type=TYPE.OPEN_SEARCH;
			}
			if(type==TYPE.OPEN_LIST || type==TYPE.OPEN_SEARCH ){
				level=0;
				length=this.opts.max_height/item_height+1;
				if(length>dataTable.length){
					length=dataTable.length;
				}
			}else if(type==TYPE.SCROLL){
				level=0;
				length=this.opts.max_height/item_height+1;
				if(until>=0){
					length=until;
				}
				if(length>dataTable.length){
					length=dataTable.length;
				}
			}else if(type==TYPE.OPEN_TREE){
				level=parseInt(dataTable[idex][0]);
			}
			for(var i=0; i<level; i++){
				lspace+=ICON_SIZE;
			}
			var counter=0;
			var i=idex+1;
			var tableLength=dataTable.length;
			while(counter<length && i<tableLength){
				var li=jQuery("<li><span class='combo-node'>"+dataTable[i][2]+"</span></li>");
				if(type==TYPE.OPEN_SEARCH){
					li.prepend("<span class='combo-spacer' style='width:"+lspace+"px' ></span>");
					li.data({index:i, level:dataTable[i][0], code:dataTable[i][1], value:dataTable[i][2]});
				}else {
					if(dataTable[i][0]==(level+1)){
						if(dataTable[i+1] && dataTable[i+1][0]>dataTable[i][0]){
							li.prepend(jQuery("<img width='"+ICON_SIZE+"' src='"+this.opts.icons.expand+"' class='combo-expcol' />").bind('click', {idx:i}, function(event){
								var el=jQuery(this).parent();
								if(el.data().opened==false){
									ref.openLevel(event.data.idx, dataTable, el);
								}else{
									ref.closeLevel(el, el.data().level+1);
								}
								event.stopPropagation();
							}));
							li.prepend("<span class='combo-spacer' style='width:"+(lspace-ICON_SIZE)+"px;' ></span>");
							li.data({index:i, level:dataTable[i][0], code:dataTable[i][1], value:dataTable[i][2], opened:false});
						}else{
							li.prepend("<span class='combo-spacer' style='width:"+lspace+"px' ></span>");
							li.data({index:i, level:dataTable[i][0], code:dataTable[i][1], value:dataTable[i][2]});
						}
					}else if(dataTable[i][0]<(level+1)){
						break;
					}else{
						i++;
						continue;
					}
				}
				li.bind('mouseover', function(){
					var li=list.find('ul li:eq('+index+')');
					li.removeClass('combo-selected');
					li=jQuery(this);
					li.addClass('combo-selected');
					index=li.index();
				}).bind('click', function(){
					var li=list.find('ul li:eq('+index+')');
					if(ref.opts.select_categories){
						ref.commit(li);
						ref.close(true);
					}else{
						if(li.data().opened==true || li.data().opened==false){
							li.children('img:first').click();
						}else{
							ref.commit(li);
							ref.close(true);
						}
					}
				});
				if(idex<0){
					dest.append(li);
				}else{
					dest.after(li);
					dest=li;
				}
				counter++;
				i++;
			}
		}
		this.closeLevel=function(node, level){
			if(node.data().level!=level){
				if(node.data().opened){
					while(node.next().length>0 && node.next().data().level>=level){
						node.next().remove();
					}
					node.find("img:first").attr('src', ref.opts.icons.expand);
					node.data().opened=false;
				}
			}else{
				/*while(node.next().length>0 && node.next().data().level>=level){
					node.next().remove();
				}*/
				var tmp=node.prev();
				while(tmp.data().level>=level){
					tmp=tmp.prev();
				}
				ref.moveAt(tmp.index());
				/*var parent=node.prev();
				node.remove();
				parent.find("img:first").attr('src', ref.opts.icons.expand);
				parent.addClass('combo-selected');
				parent.data().opened=false;
				index=parent.index();*/
			}
		}
		this.openLevel=function(idx, dataTable, node){
			if(!node.data().opened){
				ref.populateLevel(idx, dataTable, node, TYPE.OPEN_TREE);
				jQuery(node).find("img:first").attr('src', ref.opts.icons.collpase);
				node.data().opened=true;
			}
		}
		this.scrollItems=function(until){
			var lastli=list.find('ul li:last');
			var uelen=lastli.data().index;
			var dataTable=getDataTable();
			ref.populateLevel(uelen, dataTable, lastli, TYPE.SCROLL, until);
		}
		this.addSelect= function(sel) {
			var options=sel.options;
			for(var i=0; i<options.length; i++){
				data[i]=[];
				data[i][0]='1';
				if(options[i].value){
					data[i][1]=options[i].value;
				}else{
					data[i][1]=options[i].text;
				}
				data[i][2]=options[i].text;
			}
		}
		/*
		json={"data":[[row1, level, code, value]
									[row2, level, code, value]
									[row3, level, code, value]
								 ]};
		*/
		this.addJson= function(jsonData) {
			var dat=jsonData.data;
			var length=dat.length;
			for(var i=0; i<length; i++){
				var jsn=dat[i];
				data[i]=[];
				data[i][0]=jsn.level;
				data[i][1]=jsn.value;
				data[i][2]=jsn.text;
			}
		}
		/*
		<elements>
			<item level='1' code='one' >value one</item>
			<item level='1' code='two' >value two</item>
		<elements>
		*/
		this.addXml= function(xmlData) {
			var items=jQuery(xmlData).find('item');
			var length=items.length;
			for(var i=0; i<length; i++){
				var ro=jQuery(items[i]);
				data[i]=[];
				data[i][0]=ro.attr('level');
				data[i][1]=ro.attr('value');
				data[i][2]=ro.text();
			}
		}
		this.open=function(search){
			jQuery(arrow).css('background-image', 'url('+basepath+themes_folder+ref.opts.skin+'/combo_arrowDown.png)');
			if(search){
				tmpData=this.search(input.val());
				ref.populateList();
			}else{
				tmpData=[];
				ref.populateList();
			}
			var length=this.opts.max_height/item_height;
			var dataTable=getDataTable();
			if(dataTable.length<length){
				length=item_height*dataTable.length;
			}else{
				length=this.opts.max_height;
			}
			var jq=input.parent();
			var offset = jq.offset();
			var pos = jq.position();
			var topOffset=jq.scrollTop();
			list.css('left', pos.left-3);//3 top left width
			list.css('top', topOffset+pos.top);
			list.css('width', jq.innerWidth()+3+18-2);//3 to left, 18 to right, -2 to border
			list.css('height', length);
			list.css('overflow', 'auto');
			list.css('z-index','10');
			jQuery(list).slideDown(100);
			ref.moveAt(0);
			jQuery(document).bind("keydown", function(event){
				switch(event.keyCode){
						case KEY_CODES.ENTER:
								if(opened){
									var elem=list.find('ul li:eq('+index+')');
									if(ref.opts.select_categories){
										ref.commit(elem);
										ref.close(true);
									}else{
										if(typeof elem.data().opened !=  "undefined"){
											ref.openLevel(elem.data().index, getDataTable(), elem);
										}else{
											ref.commit(elem);
											ref.close(true);
										}
									}
								}
							break;
						case KEY_CODES.UP:
								ref.move(-1);
								event.preventDefault();
							break;
						case KEY_CODES.DOWN:
								ref.move(1);
								event.preventDefault();
							break;
						case KEY_CODES.LEFT:
								var elem=list.find('ul li:eq('+index+')');
								var level=elem.data().level;
								if(elem.data().opened){
									ref.closeLevel(elem, level+1);
								}else{
									if(level==1){
										ref.move(-1);
									}else{
										ref.closeLevel(elem, level);
									}
								}
							break;
						case KEY_CODES.RIGHT:
								var elem=list.find('ul li:eq('+index+')');
								if(typeof elem.data().opened !=  "undefined" && !elem.data().opened){
									ref.openLevel(elem.data().index, getDataTable(), elem);
								}else{
									ref.move(1);
								}
							break;
						case KEY_CODES.PAGE_UP:
								ref.move(-ref.opts.page_step);
								event.preventDefault();
							break;
						case KEY_CODES.PAGE_DOWN:
								ref.move(ref.opts.page_step);
								event.preventDefault();
							break;
						case KEY_CODES.ESC:
								ref.close(true);
							break;
						default:
							break;
				}
			});
			if(this.opts.editable){
				input.blur();
			}
			wrapper.parent().bind( "clickoutside", function(event){
				if(ref.opts.auto_close){
					ref.close(false);
				}
			});
			opened=true;
		}
		this.close=function(focus){
			if(!list || !list.is(':visible')){
				return;
			}
			list.get(0).innerHTML="";
			wrapper.parent().unbind("clickoutside");
			jQuery(list).slideUp(100, function(){
				list.remove();
				list=null;
				opened=false;
				jQuery(document).unbind("keydown");
				if(ref.opts.editable){
					if(focus){
						input.focus();
					}
				}
				jQuery(arrow).css('background-image', 'url('+basepath+themes_folder+ref.opts.skin+'/combo_arrowUp.png)');
			});
		}
		this.move=function(step){
			var ul=list.find('ul');
			ul.find('li:eq('+index+')').removeClass('combo-selected');
			var dataTable=getDataTable();
			if(index+step<0){
				index=dataTable.length-1;
				var lilength=ul.find('li').length;
				if(ul.find('li').length<=index){
					ref.scrollItems(index);
				}
				lilength=ul.find('li').length;
				if(lilength<=index){
					index=lilength-1;
				}
			}else if(index+step>=dataTable.length){
				index=0;
			}else{
				index+=step;
				var lilength=ul.find('li').length;
				if(lilength<=index){
					ref.scrollItems(index);
				}
				lilength=ul.find('li').length;
				if(lilength<=index){
					index=0;
				}
			}
			var elli=ul.find('li:eq('+index+')');
			var pos=elli.position();
			if(pos.top<0){
				list.scrollTop(list.scrollTop()+pos.top);
			}else if(pos.top+elli.innerHeight()>this.opts.max_height){
				if(index==ul.children().length-1){
					list.scrollTop(list.scrollTop()+pos.top);
				}else{
					list.scrollTop(list.scrollTop()+(step*elli.innerHeight()));
				}
			}
			elli.addClass('combo-selected');
		}
		this.moveAt=function(pos){
			var ul=list.find('ul');
			ul.find('li:eq('+index+')').removeClass('combo-selected');
			var dataTable=getDataTable();
			if(pos<0){
				pos=0;
			}else if(pos>=dataTable.length){
				pos=dataTable.length-1;
			}
			index=pos;
			var elli=ul.find('li:eq('+index+')');
			list.scrollTop(0);
			list.scrollTop(elli.position().top);
			elli.addClass('combo-selected');
		}
		this.commit=function(element){
			if(this.opts.editable){
				input.val(element.data().value);
			}else{
				input.html(element.data().value);
			}
			input.data('code', element.data().code);
		}
		this.search=function(value){
			if(value==''){
				return [];
			}
			var results=[];
			for(var i=0; i<data.length; i++){
				if(data[i][2].indexOf(value)>=0){
					results.push(data[i]);
				}
			}
			return results;
		}
		/*this.refreshSearch=function(){
			var value=input.attr('value');
			var dataTable=getDataTable();
			tmpData=search(value);
		}*/
		this.getText=function(){
			if(this.opts.editable){
				return input.val();
			}
			return input.html();
		}
		
		this.getValue=function(){
			var code=input.data('code');
			if(code){
				return code;
			}
			return this.getText();
		}
		this.destroy=function(revert){
			var obj=el.get(0);
			if(obj.tagName=="SELECT" || obj.tagName=="INPUT"){
				if(revert==true){
					el.css('display', '');
				}
				el.next().remove();
			}else{
				el.empty();
			}
			Combos.remove_combo(this);
		}
		function getDataTable(){
			if(tmpData.length>0){
				return tmpData;
			}else{
				return data;
			}
		}
		function getPath(){
			//find basepath from script tag
			var path = '';
			var scripts = document.getElementsByTagName( 'script' );
			for ( var i = 0 ; i < scripts.length ; i++ ){
				//var regExp="(^|.*[\\\\\\/])"+fileName+"?.js(?:\\?.*)?$";
				var match = scripts[i].src.match(/(^|.*[\\\/])jquery.combo?.js(?:\?.*)?$/i);
				if ( match ){
					path = match[1];
					break;
				}
			}
			return path;
		}
		this.getInput=function(){
			return input;
		}		

	}
})(jQuery);
