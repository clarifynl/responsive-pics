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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,o=!1,a={width:0,height:0},c=function(e){var o=wp.media.template("attachment-select-focal-point"),a=e.find(".thumbnail"),c=e.find(".details-image");o&&(a.prepend(o),e.find(".image-focal"),i=e.find(".image-focal__wrapper"),n=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),c.prependTo(i),t=i.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),s=e.find(".attachment-actions");p&&s.append(p)},p=function(t){var i=t.get("compat");if(i.item)return{x:e(i.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:e(i.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},s=function(t,i){console.log("setFocalPoint",t,i),n.css({left:"".concat(t,"%"),top:"".concat(i,"%"),display:"block"})},r=function(t){o=!0,e("body").addClass("focal-point-dragging")},d=function(t){if(t.preventDefault(),o){console.log(e(t.currentTarget).position());var i=(n=e(t.currentTarget).position(),console.log(Number(n.left/a.width*100).toFixed(2)),{x:Number(n.left/a.width*100).toFixed(2),y:Number(n.top/a.height*100).toFixed(2)});s(i.x,i.y)}var n},l=function(t){e("body").removeClass("focal-point-dragging"),o=!1},f=function(t){a={width:t.width(),height:t.height()},i.css({width:"".concat(a.width,"px"),height:"".concat(a.height,"px")})},m=function(i){var o=p(i);s(o.x,o.y),t.on("load",(function(t){return f(e(t.currentTarget))})),e(window).on("resize",(function(){return f(t)})),n.on("mousedown",r),n.on("mousemove",d),n.on("mouseup",l)},u=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=u.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(c(this.$el),m(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=p(this.model);s(t.x,t.y)}}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-e70d8053.js.map