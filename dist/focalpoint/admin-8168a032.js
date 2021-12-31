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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,o,a=!1,c={width:0,height:0},p=function(e){var o=wp.media.template("attachment-select-focal-point"),a=e.find(".thumbnail"),c=e.find(".details-image");o&&(a.prepend(o),e.find(".image-focal"),i=e.find(".image-focal__wrapper"),n=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),c.prependTo(i),t=i.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),s=e.find(".attachment-actions");p&&s.append(p)},s=function(t){var i=t.get("compat");if(i.item)return{x:e(i.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:e(i.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},d=function(t,i){console.log("setFocalPoint",t,i),n.css({left:"".concat(t,"%"),top:"".concat(i,"%"),display:"block"})},f=function(t){a=!0,e("body").addClass("focal-point-dragging"),o=i.offset()},r=function(t){if(t.preventDefault(),a){var i={x:t.pageX-o.left,y:t.pageY-o.top},e=(n=i,{x:Number(n.x/c.width*100).toFixed(2),y:Number(n.y/c.height*100).toFixed(2)});d(e.x,e.y)}var n},l=function(t){e("body").removeClass("focal-point-dragging"),a=!1},m=function(t){c={width:t.width(),height:t.height()},i.css({width:"".concat(c.width,"px"),height:"".concat(c.height,"px")})},u=function(i){var o=s(i);d(o.x,o.y),t.on("load",(function(t){return m(e(t.currentTarget))})),e(window).on("resize",(function(){return m(t)})),n.on("mousedown",f),n.on("mousemove",r),n.on("mouseup",l)},h=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=h.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(p(this.$el),u(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=s(this.model);d(t.x,t.y)}}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-8168a032.js.map