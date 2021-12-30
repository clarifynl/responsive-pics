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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(i,t,n){n(1),n(2),i.exports=n(3)},function(i,t,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(i,t){var n;(n=jQuery)(document).ready((function(){var i,t,e,o,a=function(i){t.addClass("is-dragging")},c=function(i){console.log("draggingFocalPoint",i.target)},p=function(i){t.removeClass("is-dragging")},s=function(i){i.stopPropagation(),i.preventDefault()},d=function(i){i.stopPropagation(),i.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(n){var a=wp.media.template("attachment-select-focal-point"),c=n.find(".thumbnail"),p=n.find(".details-image");a&&(c.prepend(a),t=n.find(".image-focal"),e=n.find(".image-focal__wrapper"),o=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),p.prependTo(e),i=e.find(".details-image"));var s=wp.media.template("attachment-save-focal-point"),d=n.find(".attachment-actions");s&&d.append(s)},r=function(t){var l,r,f=t.get("compat");if(f.item){var m=n(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),g=n(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=m,r=g,console.log(i.width(),i.height()),o.css({left:"".concat(l,"%"),top:"".concat(r,"%"),display:"block"}),e.on("dragover",s),e.on("drop",d),o.on("dragstart",a),o.on("drag",c),o.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var i=this.model.attributes.type;return"image"===i&&(l(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(i,t,n){}],[[0,1]]]);
//# sourceMappingURL=admin-2052f9b1.js.map