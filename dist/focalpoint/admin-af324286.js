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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,o={width:0,height:0},a=function(n){var o=wp.media.template("attachment-select-focal-point"),a=n.find(".thumbnail"),c=n.find(".details-image");o&&(a.prepend(o),n.find(".image-focal"),i=n.find(".image-focal__wrapper"),e=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),c.prependTo(i),t=i.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),r=n.find(".attachment-actions");p&&r.append(p)},c=function(t){var i=t.get("compat");if(i.item)return{x:n(i.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:n(i.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},p=function(t,i){console.log("setFocalPoint",t,i),e.css({left:"".concat(t,"%"),top:"".concat(i,"%"),display:"block"})},r=function(t){n("body").addClass("focal-point-dragging"),t.originalEvent.dataTransfer.effectAllowed="move"},s=function(t){n("body").removeClass("focal-point-dragging")},d=function(t){t.stopPropagation(),t.preventDefault(),t.originalEvent.dataTransfer.dropEffect="move"},f=function(t){t.stopPropagation(),t.preventDefault(),console.log(t.originalEvent.dataTransfer,e.position(),e.offset()),e.position()},l=function(t){o={width:t.width(),height:t.height()},i.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})},m=function(o){var a=c(o);p(a.x,a.y),t.on("load",(function(t){return l(n(t.currentTarget))})),n(window).on("resize",(function(){return l(t)})),i.on("dragover",d),i.on("drop",f),e.on("dragstart",r),e.on("dragend",s)},h=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=h.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(a(this.$el),m(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=c(this.model);p(t.x,t.y)}}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-af324286.js.map