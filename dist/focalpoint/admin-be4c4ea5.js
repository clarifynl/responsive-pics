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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,e,n){n(1),n(2),t.exports=n(3)},function(t,e,n){var o="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(o)]},function(t,e){var n;(n=jQuery)(document).ready((function(){var t,e,o=function(t){console.log("startDragFocalPoint",t.target),t.preventDefault()},a=function(t){console.log("draggingFocalPoint",t.target),t.preventDefault()},i=function(t){console.log("endDragFocalPoint",t.target),t.preventDefault()},c=function(t){console.log("dragOverFocalPoint",t.target),t.stopPropagation(),t.preventDefault()},p=function(t){console.log("dropFocalPoint",t.target),t.stopPropagation(),t.preventDefault()},r=function(n){var o=wp.media.template("attachment-select-focal-point"),a=n.find(".thumbnail"),i=n.find(".details-image");o&&(a.prepend(o),n.find(".image-focal"),t=n.find(".image-focal__wrapper"),e=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),i.prependTo(t));var c=wp.media.template("attachment-save-focal-point"),p=n.find(".attachment-actions");c&&p.append(c)},l=function(r){r.id;var l,s,d=r.get("compat");if(d.item){var f=n(d.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=n(d.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=f,s=m,e.css({left:"".concat(l,"%"),top:"".concat(s,"%"),display:"block"}),t.on("dragover",c),t.on("drop",p),e.on("dragstart",o),e.on("drag",a),e.on("dragend",i)}},s=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=s.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(r(this.$el),l(this.model)),this},change:function(){"image"===this.model.attributes.type&&l(this.model)}})}))},function(t,e,n){}],[[0,1]]]);
//# sourceMappingURL=admin-be4c4ea5.js.map