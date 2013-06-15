
 (function (jQuery) {
 	/**
 	 * jqGrid extension
 	 * Tim Heckel timheckel@gmail.com
 	 *
 	 * Dual licensed under the MIT and GPL licenses:
 	 * http://www.opensource.org/licenses/mit-license.php
 	 * http://www.gnu.org/licenses/gpl-2.0.html
 	 **/
 	jQuery.extend(jQuery.jgrid, {
 		//overrides existing
 		getCellIndex: function (cell) {
 			var c = jQuery(cell);
 			if (c.is('tr')) { return -1; }

 			var index = -1;
 			if (c.parent().hasClass("ui-jqgrid-frozenColumnHeader")) {
 				index = parseInt(c.attr("rel"));
 			} else {
 				c = (!c.is('td') && !c.is('th') ? c.closest("td,th") : c)[0];
 				if (jQuery.browser.msie) {
 					index = jQuery.inArray(c, c.parentNode.cells);
 				} else {
 					index = c.cellIndex;
 				}
 			}
 			return index;
 		}
 	});

 	jQuery.jgrid.extend({
 		removeFreezeIcons: function () {
 			console.log(1);
 			jQuery(".toggleFrozenColumn").remove();
 		},
 		freezingSetup: function () {
 			return this.each(function () {
 				var grid = this;
 				var colIndex = -1;
 				jQuery(".ui-th-column").each(function () {
 					colIndex++;
 					if (colIndex <= grid.p.colModel.length - 1) {
 						if (jQuery(this).is(":visible") && grid.p.colModel[colIndex].freezing) {
 							if (jQuery(".toggleFrozenColumn[rel=" + colIndex + "]").length === 0) {
 								jQuery(this).children(".ui-jqgrid-sortable").prepend("<img href='javascript:' class='toggleFrozenColumn' rel='" + colIndex + "' src='" + grid.p.imageDir + "/pinup.png'/>");
 							}
 						}
 					} else {
 						return;
 					}
 				});

 				jQuery(".toggleFrozenColumn").unbind();
 				jQuery(".toggleFrozenColumn").click(function (e) {
 					e.stopPropagation();
 					var img = this;
 					jQuery.extend(grid.p, { currentFrozenIndex: parseInt(jQuery(this).attr("rel")) });
 					process(img);
 				});

 				function process(img) {
 					jQuery(".toggleFrozenColumn").hide();
 					jQuery(img).show();
 					var _ci = parseInt(jQuery(img).attr("rel"));
 					var _name = grid.p.colModel[parseInt(jQuery(img).attr("rel"))].name;
 					if (jQuery(img).attr("src").indexOf("pindown") > -1) {
 						jQuery("#" + grid.p.id + "_" + _name).find('.toggleFrozenColumn').attr("src", grid.p.imageDir + "/pinup.png");
 						for (ci = parseInt(jQuery(img).attr("rel")); ci > -1; ci--) {
 							jQuery("#" + grid.p.id).jqGrid("unfreezeColumn", ci);
 						}
 						jQuery(".toggleFrozenColumn").show();
 					} else {
 						jQuery(img).attr("src", grid.p.imageDir + "/pindown.png");
 						for (ci = parseInt(jQuery(img).attr("rel")); ci > -1; ci--) {
 							jQuery("#" + grid.p.id).jqGrid("freezeColumn", ci);
 						}
 					}
 				}
 				if (grid.p.currentFrozenIndex !== undefined) {
 					var img = jQuery(".toggleFrozenColumn[rel=" + grid.p.currentFrozenIndex + "]");

 					//unfreeze all
 					process(img[img.length - 1]);

 					//freeze all
 					process(img[0]);
 				}
 			});
 		},
 		unfreezeColumn: function (colIndex) {
 			return this.each(function () {
 				var t = this;
 				var _name = t.p.colModel[colIndex].name;
 				if (jQuery("#frozenColumn_" + _name).length > 0) {
 					jQuery("#frozenColumnHeader_" + _name).remove();
 					jQuery("#frozenColumn_" + _name).remove();
 				}
 			});
 		},
 		freezeColumn: function (colIndex) {
 			jQuery(".ui-jqgrid-bdiv").scroll(function () {
 				jQuery(".ui-jqgrid-frozenColumn").scrollTop(jQuery(this).scrollTop());
 			});
 			return this.each(function () {
 				var t = this;
 				var _name = t.p.colModel[colIndex].name;
 				var _allow = t.p.colModel[colIndex].freezing;
 				if (jQuery("#frozenColumn_" + _name).length === 0 && _allow) {
 					var _id = t.p.id;
 					var c = jQuery("#" + _id + "_" + _name);
 					if (c.is(":visible")) {
 						var th = c.clone(true).css("height", c.height() + "px").css("vertical-align", "middle"); //.css("background-color", t.p.frozenColumnHorizontalBorderColor); //.css("font-weight", c.css("font-weight"));

 						var ct = "";

 						var cell = jQuery('td[aria-describedby=' + _id + '_' + _name + ']');
 						var dimen = { height: 0, top: 0, width: 0, top: 0, left: 0 };
 						dimen.height = jQuery(".ui-jqgrid-bdiv").outerHeight(true) - 16;
 						jQuery.each(cell, function () {
 							var cls = new Array();
 							var classList = $(this).attr('class').split(/s+/);
 							jQuery.each(classList, function (index, item) {
 								cls.push(item);
 							});
 							ct += "<div class='ui-jqgrid-frozenColumnCell " + cls.join(' ') + "' style='border-right:1px solid " + t.p.frozenColumnVerticalBorderColor + ";border-left:1px solid transparent;border-bottom:1px solid " + t.p.frozenColumnHorizontalBorderColor + ";background-color:#FFF;padding-top:1px;height:" + (jQuery(this).height() - 1) + "px;'>" + jQuery(this).html() + "</div>";
 							if (dimen.width === 0) dimen.width = jQuery(this).width() + 1;
 							if (dimen.top === 0) {
 								dimen.top = jQuery(".ui-jqgrid-titlebar").outerHeight(true) + c.outerHeight(true) + 1;
 								dimen.left = jQuery(this).position().left - 1;
 							}
 						});

 						var titleBarHeight = jQuery(".ui-jqgrid-titlebar").outerHeight(true);

 						jQuery(".ui-jqgrid-view").append("<div class='ui-jqgrid-frozenColumnHeader' id='frozenColumnHeader_" + _name + "' style='height:" + c.height() + "px;width:" + c.width() + "px;top:" + titleBarHeight + "px;left:" + dimen.left + "px;position:absolute;overflow:hidden;border-right:1px solid " + t.p.frozenColumnVerticalBorderColor + "'></div>");
 						jQuery(".ui-jqgrid-view").append("<div class='ui-jqgrid-frozenColumn' id='frozenColumn_" + _name + "' style=';overflow:hidden;position:absolute;height:" + dimen.height + "px;width:" + dimen.width + "px;top:" + dimen.top + "px;left:" + dimen.left + "px;'>" + ct + "</div>");

 						c = (!c.is('td') && !c.is('th') ? c.closest("td,th") : c)[0];

 						if (jQuery.browser.msie) {
 							th.attr("rel", jQuery.inArray(jQuery("#" + _id + "_" + _name), c.parentNode.cells));
 						} else {
 							th.attr("rel", c.cellIndex);
 						}
 						jQuery("#frozenColumnHeader_" + _name).append(th);
 					}
 				}
 			});
 		}
 	});
 })(jQuery);