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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,a,o={width:0,height:0},c=function(t){i.addClass("is-dragging"),t.originalEvent.dataTransfer.effectAllowed="move"},p=function(t){i.removeClass("is-dragging")},r=function(t){t.stopPropagation(),t.preventDefault(),t.originalEvent.dataTransfer.dropEffect="move"},d=function(t){t.originalEvent.stopPropagation(),t.originalEvent.preventDefault(),console.log("dropFocalPoint",a.position())},s=function(e){var o=wp.media.template("attachment-select-focal-point"),c=e.find(".thumbnail"),p=e.find(".details-image");o&&(c.prepend(o),i=e.find(".image-focal"),n=e.find(".image-focal__wrapper"),a=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),p.prependTo(n),t=n.find(".details-image"));var r=wp.media.template("attachment-save-focal-point"),d=e.find(".attachment-actions");r&&d.append(r)},l=function(i){var s,l,f=i.get("compat");if(f.item){var m=e(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),g=e(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();s=m,l=g,t.on("load",(function(t){o={width:e(t.currentTarget).width(),height:e(t.currentTarget).height()},n.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})})),a.css({left:"".concat(s,"%"),top:"".concat(l,"%"),display:"block"}),n.on("dragover",r),n.on("drop",d),a.on("dragstart",c),a.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(s(this.$el),l(this.model)),this},change:function(){"image"===this.model.attributes.type&&l(this.model)}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-30dac612.js.map