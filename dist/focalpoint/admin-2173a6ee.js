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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,o,a=function(t){i.addClass("is-dragging")},c=function(t){console.log("draggingFocalPoint",t.target)},p=function(t){i.removeClass("is-dragging")},s=function(t){t.stopPropagation(),t.preventDefault()},d=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",o.position())},r=function(n){var a=wp.media.template("attachment-select-focal-point"),c=n.find(".thumbnail"),p=n.find(".details-image");a&&(c.prepend(a),i=n.find(".image-focal"),e=n.find(".image-focal__wrapper"),o=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),p.prependTo(e),t=e.find(".details-image"));var s=wp.media.template("attachment-save-focal-point"),d=n.find(".attachment-actions");s&&d.append(s)},l=function(i){var r,l,f=i.get("compat");if(f.item){var m=n(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),g=n(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();r=m,l=g,console.log(t,t.outerWidth(),t.outerHeight()),o.css({left:"".concat(r,"%"),top:"".concat(l,"%"),display:"block"}),e.on("dragover",s),e.on("drop",d),o.on("dragstart",a),o.on("drag",c),o.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(r(this.$el),l(this.model)),this},change:function(){"image"===this.model.attributes.type&&l(this.model)}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-2173a6ee.js.map