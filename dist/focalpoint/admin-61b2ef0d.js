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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,o={width:0,height:0},a=function(e){var o=wp.media.template("attachment-select-focal-point"),a=e.find(".thumbnail"),c=e.find(".details-image");o&&(a.prepend(o),e.find(".image-focal"),i=e.find(".image-focal__wrapper"),n=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),c.prependTo(i),t=i.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),r=e.find(".attachment-actions");p&&r.append(p)},c=function(t){var i=t.get("compat");if(i.item)return{x:e(i.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:e(i.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},p=function(t,i){console.log(t,i),n.css({left:"".concat(t,"%"),top:"".concat(i,"%"),display:"block"})},r=function(t){e("body").addClass("focal-point-dragging"),t.originalEvent.dataTransfer.effectAllowed="move"},d=function(t){e("body").removeClass("focal-point-dragging")},s=function(t){t.stopPropagation(),t.preventDefault(),t.originalEvent.dataTransfer.dropEffect="move"},f=function(t){t.stopPropagation(),t.preventDefault();var i,e=(i=n.position(),{x:Number.parseFloat(i.left/o.width*100).toFixed(2),y:Number.parseFloat(i.top/o.height*100).toFixed(2)});p(e.x,e.y)},l=function(t){o={width:t.width(),height:t.height()},i.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})},m=function(o){var a=c(o);p(a.x,a.y),t.on("load",(function(t){return l(e(t.currentTarget))})),e(window).on("resize",(function(){return l(t)})),i.on("dragover",s),i.on("drop",f),n.on("dragstart",r),n.on("dragend",d)},h=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=h.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(a(this.$el),m(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=c(this.model);p(t.x,t.y)}}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-61b2ef0d.js.map