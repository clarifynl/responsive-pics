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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,n,e){e(1),e(2),t.exports=e(3)},function(t,n,e){var o="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(o)]},function(t,n){var e;(e=jQuery)(document).ready((function(){var t,n,o=function(t){console.log("startDragFocalPoint",t.target)},i=function(t){console.log("draggingFocalPoint",t.target)},a=function(t){console.log("endDragFocalPoint",t.target)},c=function(t){console.log("dragOverFocalPoint",t.target),t.preventDefault()},p=function(t){console.log("dropFocalPoint",t.target),t.preventDefault()},l=function(e){var o=wp.media.template("attachment-select-focal-point"),i=e.find(".thumbnail"),a=e.find(".details-image");o&&(i.prepend(o),e.find(".image-focal"),t=e.find(".image-focal__wrapper"),n=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),a.prependTo(t));var c=wp.media.template("attachment-save-focal-point"),p=e.find(".attachment-actions");c&&p.append(c)},r=function(l){l.id;var r,s,d=l.get("compat");if(d.item){var f=e(d.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=e(d.item).find(".compat-field-responsive_pics_focal_point_y input").val();r=f,s=m,n.css({left:"".concat(r,"%"),top:"".concat(s,"%"),display:"block"}),t.on("dragover",c),t.on("drop",p),n.on("dragstart",o),n.on("drag",i),n.on("dragend",a)}},s=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=s.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(t,n,e){}],[[0,1]]]);
//# sourceMappingURL=admin-65379ff5.js.map