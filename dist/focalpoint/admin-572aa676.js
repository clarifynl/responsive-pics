/*!
 * 
 * ResponsivePics
 * 
 * @author Booreiland
 * @version 1.4.0
 * @link https://responsive.pics
 * @license undefined
 * 
 * Copyright (c) 2021 Booreiland
 * 
 * This software is released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE)
 */
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,e,i){i(1),i(2),t.exports=i(3)},function(t,e,i){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(n)]},function(t,e){var i;(i=jQuery)(document).ready((function(){var t,e,n,o=!1,a={x:0,y:0},c={width:0,height:0},p=function(i){var o=wp.media.template("attachment-select-focal-point"),a=i.find(".thumbnail"),c=i.find(".details-image");o&&(a.prepend(o),i.find(".image-focal"),e=i.find(".image-focal__wrapper"),n=i.find(".image-focal__point"),i.find(".image-focal__clickarea"),c.prependTo(e),t=e.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),s=i.find(".attachment-actions");p&&s.append(p)},s=function(t){var e=t.get("compat");if(e.item)return{x:i(e.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:i(e.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},r=function(t,e){console.log("setFocalPoint",t,e),n.css({left:"".concat(t,"%"),top:"".concat(e,"%"),display:"block"})},d=function(t){o=!0,i("body").addClass("focal-point-dragging");var e=i(t.currentTarget).offset();a={x:e.left-t.pageX,y:e.top-t.pageY}},f=function(t){if(t.preventDefault(),o){var e={x:t.pageX+a.x,y:t.pageY+a.y};console.log(a,e);var i=(n=e,{x:Number(n.x/c.width*100).toFixed(2),y:Number(n.y/c.height*100).toFixed(2)});r(i.x,i.y)}var n},l=function(t){i("body").removeClass("focal-point-dragging"),o=!1},m=function(t){c={width:t.width(),height:t.height()},e.css({width:"".concat(c.width,"px"),height:"".concat(c.height,"px")})},u=function(e){var o=s(e);r(o.x,o.y),t.on("load",(function(t){return m(i(t.currentTarget))})),i(window).on("resize",(function(){return m(t)})),n.on("mousedown",d),n.on("mousemove",f),n.on("mouseup",l)},h=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=h.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(p(this.$el),u(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=s(this.model);r(t.x,t.y)}}})}))},function(t,e,i){}],[[0,1]]]);
//# sourceMappingURL=admin-572aa676.js.map